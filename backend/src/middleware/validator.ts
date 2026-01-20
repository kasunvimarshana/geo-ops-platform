import Joi from 'joi';
import { Request, Response, NextFunction } from 'express';
import { AppError } from '../utils/errors';

export const validate = (schema: Joi.ObjectSchema) => {
  return (req: Request, res: Response, next: NextFunction) => {
    const { error } = schema.validate(req.body, { abortEarly: false });

    if (error) {
      const message = error.details.map(detail => detail.message).join(', ');
      return next(new AppError(400, message));
    }

    next();
  };
};
