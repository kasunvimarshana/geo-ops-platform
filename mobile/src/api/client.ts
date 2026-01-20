import axios from 'axios';
import { API_BASE_URL } from '../constants/Config';

// Create an instance of axios with default settings
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000, // Set a timeout for requests
});

// Add a request interceptor
apiClient.interceptors.request.use(
  (config) => {
    // You can add authorization tokens or other headers here
    const token = ''; // Retrieve token from storage or state
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add a response interceptor
apiClient.interceptors.response.use(
  (response) => {
    return response.data; // Return only the data from the response
  },
  (error) => {
    // Handle errors globally
    console.error('API Error:', error);
    return Promise.reject(error);
  }
);

export default apiClient;