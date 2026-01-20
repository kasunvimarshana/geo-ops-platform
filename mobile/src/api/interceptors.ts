import axios from 'axios';
import { Alert } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Create an instance of axios
const apiClient = axios.create({
    baseURL: 'https://your-api-url.com/api/v1', // Replace with your API URL
    timeout: 10000, // Set a timeout for requests
});

// Request interceptor
apiClient.interceptors.request.use(
    async (config) => {
        const token = await AsyncStorage.getItem('token'); // Retrieve token from storage
        if (token) {
            config.headers.Authorization = `Bearer ${token}`; // Set the authorization header
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor
apiClient.interceptors.response.use(
    (response) => {
        return response.data; // Return only the data from the response
    },
    (error) => {
        if (error.response) {
            // Handle specific error responses
            const { status, data } = error.response;
            if (status === 401) {
                Alert.alert('Session expired', 'Please log in again.');
                // Optionally, you can clear the token and redirect to login
            } else if (status === 403) {
                Alert.alert('Access denied', data.message || 'You do not have permission to access this resource.');
            } else {
                Alert.alert('Error', data.message || 'An unexpected error occurred.');
            }
        } else {
            Alert.alert('Error', 'Network error. Please check your connection.');
        }
        return Promise.reject(error);
    }
);

export default apiClient;