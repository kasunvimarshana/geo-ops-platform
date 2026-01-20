import { Response } from 'express';
import { AuthService } from '../services/auth.service';
import { AuthRequest } from '../middleware/auth';
import { asyncHandler } from '../utils/errors';

const authService = new AuthService();

export class AuthController {
  register = asyncHandler(async (req: AuthRequest, res: Response) => {
    const result = await authService.register(req.body);
    res.status(201).json({
      status: 'success',
      data: result,
    });
  });

  login = asyncHandler(async (req: AuthRequest, res: Response) => {
    const { email, password } = req.body;
    const result = await authService.login(email, password);
    res.json({
      status: 'success',
      data: result,
    });
  });

  getProfile = asyncHandler(async (req: AuthRequest, res: Response) => {
    const profile = await authService.getProfile(req.user!.id);
    res.json({
      status: 'success',
      data: profile,
    });
  });
}
