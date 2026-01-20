import * as Yup from 'yup';

export const landMeasurementSchema = Yup.object().shape({
  area: Yup.number()
    .required('Area is required')
    .positive('Area must be a positive number'),
  coordinates: Yup.array()
    .of(
      Yup.object().shape({
        latitude: Yup.number().required('Latitude is required'),
        longitude: Yup.number().required('Longitude is required'),
      })
    )
    .required('Coordinates are required'),
});

export const jobCreationSchema = Yup.object().shape({
  title: Yup.string()
    .required('Job title is required')
    .min(3, 'Job title must be at least 3 characters long'),
  description: Yup.string()
    .required('Job description is required')
    .min(10, 'Job description must be at least 10 characters long'),
  landId: Yup.string().required('Land ID is required'),
  driverId: Yup.string().required('Driver ID is required'),
});

export const invoiceSchema = Yup.object().shape({
  customerId: Yup.string().required('Customer ID is required'),
  amount: Yup.number()
    .required('Amount is required')
    .positive('Amount must be a positive number'),
  dueDate: Yup.date().required('Due date is required'),
});

export const paymentSchema = Yup.object().shape({
  invoiceId: Yup.string().required('Invoice ID is required'),
  amount: Yup.number()
    .required('Amount is required')
    .positive('Amount must be a positive number'),
  paymentMethod: Yup.string().required('Payment method is required'),
});