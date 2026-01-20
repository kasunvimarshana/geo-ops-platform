import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { landsApi } from '../../../services/api/endpoints';
import type { Land, CreateLandRequest } from '../../../shared/types/api';

export const useLands = (params?: { page?: number; limit?: number }) => {
  return useQuery({
    queryKey: ['lands', params],
    queryFn: async () => {
      const response = await landsApi.getAll(params);
      return response.data;
    },
  });
};

export const useLand = (id: string) => {
  return useQuery({
    queryKey: ['lands', id],
    queryFn: async () => {
      const response = await landsApi.getById(id);
      return response.data.data;
    },
    enabled: !!id,
  });
};

export const useCreateLand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: CreateLandRequest) => {
      const response = await landsApi.create(data);
      return response.data.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['lands'] });
    },
  });
};

export const useUpdateLand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async ({ id, data }: { id: string; data: Partial<CreateLandRequest> }) => {
      const response = await landsApi.update(id, data);
      return response.data.data;
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['lands'] });
      queryClient.invalidateQueries({ queryKey: ['lands', variables.id] });
    },
  });
};

export const useDeleteLand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (id: string) => {
      await landsApi.delete(id);
      return id;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['lands'] });
    },
  });
};
