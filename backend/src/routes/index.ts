import { Router } from 'express';
import authRoutes from './auth.routes';
import landMeasurementRoutes from './land-measurement.routes';

const router = Router();

router.use('/auth', authRoutes);
router.use('/land-measurements', landMeasurementRoutes);

export default router;
