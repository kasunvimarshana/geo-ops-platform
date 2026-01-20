import { useCallback, useState } from 'react';
import { ApiError, ApiResponse } from '@types/index';

interface UseApiState<T> {
  data: T | null;
  isLoading: boolean;
  error: ApiError | null;
}

interface UseApiOptions {
  onSuccess?: () => void;
  onError?: (error: ApiError) => void;
}

export function useApi<T>(
  apiCall: () => Promise<ApiResponse<T>>,
  options?: UseApiOptions
) {
  const [state, setState] = useState<UseApiState<T>>({
    data: null,
    isLoading: false,
    error: null,
  });

  const execute = useCallback(async () => {
    setState({ data: null, isLoading: true, error: null });
    try {
      const response = await apiCall();
      if (response.success && response.data) {
        setState({ data: response.data, isLoading: false, error: null });
        options?.onSuccess?.();
      } else {
        const error: ApiError = {
          code: 'API_ERROR',
          message: response.error || response.message || 'Unknown error',
        };
        setState({ data: null, isLoading: false, error });
        options?.onError?.(error);
      }
    } catch (err) {
      const error: ApiError = {
        code: 'NETWORK_ERROR',
        message: err instanceof Error ? err.message : 'Unknown error',
      };
      setState({ data: null, isLoading: false, error });
      options?.onError?.(error);
    }
  }, [apiCall, options]);

  return { ...state, execute };
}
