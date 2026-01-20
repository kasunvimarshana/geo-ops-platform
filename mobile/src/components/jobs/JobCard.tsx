import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Job } from '../../types/job';

interface JobCardProps {
  job: Job;
}

const JobCard: React.FC<JobCardProps> = ({ job }) => {
  return (
    <View style={styles.card}>
      <Text style={styles.title}>{job.title}</Text>
      <Text style={styles.details}>Status: {job.status}</Text>
      <Text style={styles.details}>Assigned to: {job.driverName}</Text>
      <Text style={styles.details}>Scheduled Date: {job.scheduledDate}</Text>
      <Text style={styles.details}>Location: {job.location}</Text>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
    marginVertical: 8,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  details: {
    fontSize: 14,
    color: '#555',
  },
});

export default JobCard;