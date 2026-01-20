<?php

namespace Tests\Unit;

use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    private Handler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = app(Handler::class);
    }

    public function test_handles_model_not_found_exception(): void
    {
        $request = Request::create('/api/v1/test', 'GET');
        $exception = new ModelNotFoundException();

        $response = $this->handler->render($request, $exception);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Resource not found', $data['message']);
    }

    public function test_handles_not_found_http_exception(): void
    {
        $request = Request::create('/api/v1/nonexistent', 'GET');
        $exception = new NotFoundHttpException();

        $response = $this->handler->render($request, $exception);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Endpoint not found', $data['message']);
    }

    public function test_handles_unauthorized_http_exception(): void
    {
        $request = Request::create('/api/v1/protected', 'GET');
        $exception = new UnauthorizedHttpException('Bearer');

        $response = $this->handler->render($request, $exception);

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Unauthorized', $data['message']);
    }

    public function test_handles_validation_exception(): void
    {
        $request = Request::create('/api/v1/test', 'POST');
        
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['email' => 'invalid'],
            ['email' => 'required|email']
        );
        
        $exception = new ValidationException($validator);

        $response = $this->handler->render($request, $exception);

        $this->assertEquals(422, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Validation failed', $data['message']);
        $this->assertArrayHasKey('errors', $data);
    }

    public function test_handles_generic_exception_in_debug_mode(): void
    {
        config(['app.debug' => true]);
        
        $request = Request::create('/api/v1/test', 'GET');
        $exception = new \Exception('Test error message');

        $response = $this->handler->render($request, $exception);

        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Test error message', $data['message']);
        $this->assertArrayHasKey('trace', $data);
    }

    public function test_hides_error_details_in_production(): void
    {
        config(['app.debug' => false]);
        
        $request = Request::create('/api/v1/test', 'GET');
        $exception = new \Exception('Internal database error');

        $response = $this->handler->render($request, $exception);

        $this->assertEquals(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('An unexpected error occurred', $data['message']);
        $this->assertArrayNotHasKey('trace', $data);
    }

    public function test_handles_http_exception_with_status_code(): void
    {
        $request = Request::create('/api/v1/test', 'GET');
        $exception = new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Forbidden');

        $response = $this->handler->render($request, $exception);

        $this->assertEquals(403, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Forbidden', $data['message']);
    }

    public function test_returns_json_for_api_requests(): void
    {
        $request = Request::create('/api/v1/test', 'GET');
        $exception = new \Exception('Test');

        $response = $this->handler->render($request, $exception);

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function test_returns_json_when_expects_json(): void
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept', 'application/json');
        $exception = new \Exception('Test');

        $response = $this->handler->render($request, $exception);

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }
}
