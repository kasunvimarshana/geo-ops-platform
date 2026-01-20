import { Router } from 'express';
import Joi from 'joi';
import { AuthController } from '../controllers/auth.controller';
import { validate } from '../middleware/validator';
import { authenticate } from '../middleware/auth';

const router = Router();
const authController = new AuthController();

const registerSchema = Joi.object({
  email: Joi.string().email().required(),
  password: Joi.string().min(8).required(),
  firstName: Joi.string().required(),
  lastName: Joi.string().required(),
  phone: Joi.string().required(),
  role: Joi.string().valid('admin', 'owner', 'driver', 'broker', 'accountant').optional(),
  organizationName: Joi.string().optional(),
});

const loginSchema = Joi.object({
  email: Joi.string().email().required(),
  password: Joi.string().required(),
});

router.post('/register', validate(registerSchema), authController.register);
router.post('/login', validate(loginSchema), authController.login);
router.get('/profile', authenticate, authController.getProfile);

export default router;
