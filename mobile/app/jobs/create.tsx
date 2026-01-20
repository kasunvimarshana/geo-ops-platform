import React, { useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet } from 'react-native';
import { useJobs } from '../../hooks/useJobs';
import { Job } from '../../types/job';

const CreateJobScreen = () => {
    const { createJob } = useJobs();
    const [jobDetails, setJobDetails] = useState<Job>({
        title: '',
        description: '',
        landId: '',
        driverId: '',
        // Add other necessary fields based on your Job type
    });

    const handleInputChange = (name: string, value: string) => {
        setJobDetails({ ...jobDetails, [name]: value });
    };

    const handleSubmit = async () => {
        try {
            await createJob(jobDetails);
            // Handle successful job creation (e.g., navigate back or show a success message)
        } catch (error) {
            // Handle error (e.g., show an error message)
        }
    };

    return (
        <View style={styles.container}>
            <Text style={styles.title}>Create Job</Text>
            <TextInput
                style={styles.input}
                placeholder="Job Title"
                value={jobDetails.title}
                onChangeText={(value) => handleInputChange('title', value)}
            />
            <TextInput
                style={styles.input}
                placeholder="Description"
                value={jobDetails.description}
                onChangeText={(value) => handleInputChange('description', value)}
            />
            <TextInput
                style={styles.input}
                placeholder="Land ID"
                value={jobDetails.landId}
                onChangeText={(value) => handleInputChange('landId', value)}
            />
            <TextInput
                style={styles.input}
                placeholder="Driver ID"
                value={jobDetails.driverId}
                onChangeText={(value) => handleInputChange('driverId', value)}
            />
            {/* Add other input fields as necessary */}
            <Button title="Create Job" onPress={handleSubmit} />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 20,
    },
    title: {
        fontSize: 24,
        marginBottom: 20,
    },
    input: {
        height: 40,
        borderColor: 'gray',
        borderWidth: 1,
        marginBottom: 15,
        paddingHorizontal: 10,
    },
});

export default CreateJobScreen;