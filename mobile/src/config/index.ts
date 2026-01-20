export const config = {
  // Use HTTPS by default for production security
  // For local development, set API_BASE_URL=http://localhost:8000/api/v1 in .env
  apiBaseUrl: process.env.API_BASE_URL || "https://api.geo-ops.lk/api/v1",
  enableOfflineMode: true,
  syncInterval: 300000, // 5 minutes
  tokenRefreshThreshold: 300, // 5 minutes before expiry
};
