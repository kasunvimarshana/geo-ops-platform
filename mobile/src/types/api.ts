// This file defines types for API responses.

export interface ApiResponse<T> {
    success: boolean;
    message: string;
    data: T;
}

export interface AuthResponse {
    token: string;
    user: User;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

export interface Land {
    id: number;
    name: string;
    area: number; // in acres or hectares
    coordinates: string; // GeoJSON or similar format
}

export interface Job {
    id: number;
    title: string;
    description: string;
    status: string; // e.g., 'pending', 'in_progress', 'completed'
    assignedTo: number; // User ID of the assigned driver
    landId: number; // Associated land ID
}

export interface Invoice {
    id: number;
    amount: number;
    status: string; // e.g., 'draft', 'sent', 'paid', 'overdue'
    createdAt: string; // ISO date string
    updatedAt: string; // ISO date string
}

export interface Payment {
    id: number;
    invoiceId: number; // Associated invoice ID
    amount: number;
    method: string; // e.g., 'cash', 'bank_transfer', 'mobile_money'
    status: string; // e.g., 'completed', 'pending'
}