<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();
    }

    public function testCreatePayment()
    {
        $data = [
            'amount' => 100.00,
            'method' => 'Cash',
            'invoice_id' => 1,
            'customer_id' => 1,
        ];

        $payment = $this->paymentService->createPayment($data);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($data['amount'], $payment->amount);
        $this->assertEquals($data['method'], $payment->method);
    }

    public function testGetPaymentById()
    {
        $payment = Payment::factory()->create();

        $foundPayment = $this->paymentService->getPaymentById($payment->id);

        $this->assertEquals($payment->id, $foundPayment->id);
    }

    public function testUpdatePayment()
    {
        $payment = Payment::factory()->create();
        $data = [
            'amount' => 150.00,
            'method' => 'Bank Transfer',
        ];

        $updatedPayment = $this->paymentService->updatePayment($payment->id, $data);

        $this->assertEquals($data['amount'], $updatedPayment->amount);
        $this->assertEquals($data['method'], $updatedPayment->method);
    }

    public function testDeletePayment()
    {
        $payment = Payment::factory()->create();

        $this->paymentService->deletePayment($payment->id);

        $this->assertNull(Payment::find($payment->id));
    }
}