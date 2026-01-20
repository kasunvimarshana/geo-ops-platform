import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, Button } from 'react-native';
import { useRoute } from '@react-navigation/native';
import { Job } from '../../database/models/Job';
import { getJobById, updateJobStatus } from '../../api/jobs';
import GPSTracker from '../maps/GPSTracker';

const JobTracker = () => {
    const route = useRoute();
    const { jobId } = route.params;
    const [job, setJob] = useState<Job | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchJob = async () => {
            try {
                const fetchedJob = await getJobById(jobId);
                setJob(fetchedJob);
            } catch (error) {
                console.error('Error fetching job:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchJob();
    }, [jobId]);

    const handleCompleteJob = async () => {
        try {
            await updateJobStatus(jobId, 'completed');
            // Optionally navigate back or show a success message
        } catch (error) {
            console.error('Error updating job status:', error);
        }
    };

    if (loading) {
        return (
            <View style={styles.container}>
                <Text>Loading...</Text>
            </View>
        );
    }

    if (!job) {
        return (
            <View style={styles.container}>
                <Text>Job not found.</Text>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <Text style={styles.title}>Job Tracker</Text>
            <Text>Job ID: {job.id}</Text>
            <Text>Status: {job.status}</Text>
            <Text>Description: {job.description}</Text>
            <GPSTracker jobId={job.id} />
            <Button title="Complete Job" onPress={handleCompleteJob} />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 20,
        justifyContent: 'center',
        alignItems: 'center',
    },
    title: {
        fontSize: 24,
        fontWeight: 'bold',
        marginBottom: 20,
    },
});

export default JobTracker;