import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { useStore } from '../store/landStore';
import { useStore as jobStore } from '../store/jobStore';

const Dashboard = () => {
    const { lands } = useStore();
    const { jobs } = jobStore();

    return (
        <View style={styles.container}>
            <Text style={styles.title}>Dashboard</Text>
            <View style={styles.metrics}>
                <Text style={styles.metric}>Total Lands: {lands.length}</Text>
                <Text style={styles.metric}>Total Jobs: {jobs.length}</Text>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 16,
        backgroundColor: '#fff',
    },
    title: {
        fontSize: 24,
        fontWeight: 'bold',
        marginBottom: 16,
    },
    metrics: {
        marginTop: 20,
    },
    metric: {
        fontSize: 18,
        marginVertical: 8,
    },
});

export default Dashboard;