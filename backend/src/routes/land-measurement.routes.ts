import { Router } from 'express';
import Joi from 'joi';
import { LandMeasurementController } from '../controllers/land-measurement.controller';
import { validate } from '../middleware/validator';
import { authenticate } from '../middleware/auth';

const router = Router();
const controller = new LandMeasurementController();

const createSchema = Joi.object({
  name: Joi.string().required(),
  description: Joi.string().optional(),
  coordinates: Joi.array().items(
    Joi.object({
      latitude: Joi.number().required(),
      longitude: Joi.number().required(),
      timestamp: Joi.date().optional(),
    })
  ).min(3).required(),
  unit: Joi.string().valid('acres', 'hectares', 'square_meters').required(),
  address: Joi.string().optional(),
  metadata: Joi.object().optional(),
});

const updateSchema = Joi.object({
  name: Joi.string().optional(),
  description: Joi.string().optional(),
  address: Joi.string().optional(),
  metadata: Joi.object().optional(),
});

router.use(authenticate);

router.post('/', validate(createSchema), controller.create);
router.get('/', controller.getAll);
router.get('/:id', controller.getById);
router.patch('/:id', validate(updateSchema), controller.update);
router.delete('/:id', controller.delete);

export default router;
