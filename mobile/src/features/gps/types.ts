import { Coordinates, LandPlot } from '../../shared/types/api.types';

export interface MeasurementMode {
  type: 'walk' | 'point';
  isActive: boolean;
}

export interface PlotFormData {
  coordinates: Coordinates[];
  jobId?: number;
}

export interface GPSAccuracy {
  level: 'high' | 'medium' | 'low';
  meters: number;
}
