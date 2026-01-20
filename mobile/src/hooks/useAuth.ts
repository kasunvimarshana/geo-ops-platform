import { useState, useEffect } from 'react';
import { api } from '../api/auth';
import { useStore } from '../store/authStore';

const useAuth = () => {
    const { setUser, setToken } = useStore();
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const login = async (credentials) => {
        setLoading(true);
        try {
            const response = await api.post('/auth/login', credentials);
            setUser(response.data.user);
            setToken(response.data.token);
        } catch (err) {
            setError(err.response?.data?.message || 'Login failed');
        } finally {
            setLoading(false);
        }
    };

    const register = async (userData) => {
        setLoading(true);
        try {
            const response = await api.post('/auth/register', userData);
            setUser(response.data.user);
            setToken(response.data.token);
        } catch (err) {
            setError(err.response?.data?.message || 'Registration failed');
        } finally {
            setLoading(false);
        }
    };

    const logout = () => {
        setUser(null);
        setToken(null);
    };

    useEffect(() => {
        // Optionally, check for existing token and fetch user data
        const fetchUser = async () => {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const response = await api.get('/auth/me', {
                        headers: { Authorization: `Bearer ${token}` },
                    });
                    setUser(response.data);
                } catch (err) {
                    console.error('Failed to fetch user', err);
                }
            }
            setLoading(false);
        };

        fetchUser();
    }, [setUser]);

    return { login, register, logout, loading, error };
};

export default useAuth;