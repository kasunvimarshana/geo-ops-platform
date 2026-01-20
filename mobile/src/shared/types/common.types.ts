export type NetworkStatus = 'online' | 'offline' | 'unknown';

export interface LoadingState {
  isLoading: boolean;
  error: string | null;
}

export type SyncStatus = 'idle' | 'syncing' | 'success' | 'error';

export interface ValidationError {
  field: string;
  message: string;
}
