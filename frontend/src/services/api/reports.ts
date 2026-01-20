import client from './client';

const reportApi = {
  /**
   * Get financial report
   */
  getFinancial: async (params?: { from_date?: string; to_date?: string }) => {
    const response = await client.get('/reports/financial', { params });
    return response.data;
  },

  /**
   * Get jobs report
   */
  getJobs: async (params?: { from_date?: string; to_date?: string }) => {
    const response = await client.get('/reports/jobs', { params });
    return response.data;
  },

  /**
   * Get expenses report
   */
  getExpenses: async (params?: { from_date?: string; to_date?: string }) => {
    const response = await client.get('/reports/expenses', { params });
    return response.data;
  },

  /**
   * Get dashboard overview
   */
  getDashboard: async () => {
    const response = await client.get('/reports/dashboard');
    return response.data;
  },
};

export default reportApi;
