import { Response } from 'express';
import { LandMeasurementService } from '../services/land-measurement.service';
import { AuthRequest } from '../middleware/auth';
import { asyncHandler } from '../utils/errors';

const landMeasurementService = new LandMeasurementService();

export class LandMeasurementController {
  create = asyncHandler(async (req: AuthRequest, res: Response) => {
    const result = await landMeasurementService.create(req.user!.id, req.body);
    res.status(201).json({
      status: 'success',
      data: result,
    });
  });

  getById = asyncHandler(async (req: AuthRequest, res: Response) => {
    const result = await landMeasurementService.getById(req.params.id, req.user!.id);
    res.json({
      status: 'success',
      data: result,
    });
  });

  getAll = asyncHandler(async (req: AuthRequest, res: Response) => {
    const { limit, offset, search } = req.query;
    const result = await landMeasurementService.getAll(req.user!.id, {
      limit: limit ? parseInt(limit as string) : undefined,
      offset: offset ? parseInt(offset as string) : undefined,
      search: search as string,
    });
    res.json({
      status: 'success',
      data: result,
    });
  });

  update = asyncHandler(async (req: AuthRequest, res: Response) => {
    const result = await landMeasurementService.update(
      req.params.id,
      req.user!.id,
      req.body
    );
    res.json({
      status: 'success',
      data: result,
    });
  });

  delete = asyncHandler(async (req: AuthRequest, res: Response) => {
    const result = await landMeasurementService.delete(req.params.id, req.user!.id);
    res.json({
      status: 'success',
      data: result,
    });
  });
}
