import { useEffect, useState } from 'react';
import { fetchJobs } from '../api/jobs';
import { Job } from '../types/job';

const useJobs = () => {
    const [jobs, setJobs] = useState<Job[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const loadJobs = async () => {
            try {
                const fetchedJobs = await fetchJobs();
                setJobs(fetchedJobs);
            } catch (err) {
                setError('Failed to load jobs');
            } finally {
                setLoading(false);
            }
        };

        loadJobs();
    }, []);

    return { jobs, loading, error };
};

export default useJobs;