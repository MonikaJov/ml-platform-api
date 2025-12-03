# ML Platform

A comprehensive machine learning platform that combines a Laravel service for user and data management with a Flask service for machine learning operations.

## Overview

ML Platform is a backend microservices platform solution consisting of two main components:

1. **Laravel Service** (this repository): Manages user accounts, datasets, and provides tools for data organization and maintenance. It also exposes API endpoints for model predictions.

2. **Flask Service** (separate repository): Handles the machine learning operations, including model training, evaluation, and selection of the best performing models. Detailed instructions for the Flask service are available in its own repository.

This architecture separates concerns, allowing the Laravel service to focus on user experience and data management while the Flask service specializes in the computational aspects of machine learning.

## Features

### User Management
- User registration and authentication
- Token-based authentication
- User profile management

### Dataset Management
- Upload datasets
- Organize and maintain datasets
- Dataset versioning
- Access control for datasets

### Problem Definition
- Define machine learning problems (classification, regression)
- Specify target columns and problem types
- Configure training parameters

### Model Training
- Initiate model training jobs
- Asynchronous training process
- Automatic model evaluation
- Best model selection based on performance metrics

### Predictions
- Make predictions using the best trained models
- Simple API endpoint for prediction requests
- Structured prediction responses

## Architecture

The platform follows a microservices architecture with two main components:

### Laravel Service (ml-platform-api)
- **User Interface**: Provides API endpoints for frontend applications
- **Authentication**: Manages user authentication and authorization
- **Data Management**: Handles dataset storage and organization
- **API Gateway**: Communicates with the Flask service for ML operations
- **Prediction Endpoint**: Exposes endpoints for making predictions

### Flask Service (ml-platform-engine)
- **Model Training**: Trains various machine learning models
- **Model Evaluation**: Evaluates model performance
- **Model Selection**: Selects the best performing model
- **Model Storage**: Stores trained models for future use
- **Prediction Service**: Processes prediction requests

### Communication Flow
1. Users upload datasets and define problems through the Laravel service
2. Laravel sends training requests with datasets to the Flask service
3. Flask service trains multiple models and evaluates their performance
4. Flask service selects the best model and notifies the Laravel service
5. Laravel service stores the reference to the best model
6. Users can make prediction requests through the Laravel service
7. Laravel forwards prediction requests to the Flask service
8. Flask service processes the predictions and returns results
9. Laravel service formats and returns the prediction results to users

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL or PostgreSQL
- Redis (for queues)
- Python 3.8 or higher (for the Flask service)
- pip (for Python package management)

### Laravel Service Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/MonikaJov/ml-platform-api
   cd ml-platform-api
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ml_platform
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Start the Laravel server:
   ```bash
   php artisan serve
   ```

### Flask Service Setup
The Flask service is maintained in a separate repository. Basic setup steps are provided below, but please refer to the Flask repository's README for detailed instructions.

1. Clone the Flask service repository:
   ```bash
   git clone https://github.com/MonikaJov/ml-platform-engine
   cd ml-platform-engine
   ```

2. Follow the installation and configuration instructions in the Flask repository's README.

## Configuration

### Laravel Service Configuration
Configure the connection to the Flask service in your `.env` file:

```
ML_API_URL=http://localhost:5000/api
ML_API_TOKEN=your_secure_token
```

Additional configuration options can be found in the `config/app.php` file:

```php
'endpoints' => [
    'train' => '/train',
    'predict' => '/predict',
],
```

### Flask Service Configuration
Configure the connection to the Laravel service in your Flask `.env` file:

```
ML_PLATFORM_API_URL=http://localhost:8000/api
ML_PLATFORM_INTERNAL_AUTH_TOKEN=your_secure_token
```

## Usage

### Working with Datasets
1. **Upload a Dataset**:
   ```http
   POST /api/datasets
   ```
   Include the dataset file in the request.

2. **List Datasets**:
   ```http
   GET /api/datasets
   ```

3. **Update a Dataset**:
   ```http
   PUT /api/datasets/{dataset_id}
   ```
   Include the updated dataset file in the request.

4. **Delete a Dataset**:
   ```http
   DELETE /api/datasets/{dataset_id}
   ```

### Defining Problems
1. **Create a Problem Definition**:
   ```http
   POST /api/datasets/{dataset_id}/problem-details
   ```
   Include the target column and problem type in the request.

2. **Update a Problem Definition**:
   ```http
   PATCH /api/datasets/{dataset_id}/problem-details/{problem_detail_id}
   ```

### Training Models
1. **Start Model Training**:
   ```http
   POST /api/datasets/{dataset_id}/problem-details/{problem_detail_id}/best-models/train
   ```

   The system will:
   - Send the dataset to the Flask service
   - Train multiple models
   - Evaluate model performance
   - Select the best model
   - Store the best model information

2. **Training Process**:
   - The training process is asynchronous
   - The Flask service will notify the Laravel service when training is complete
   - The best model will be automatically stored and available for predictions

### Making Predictions
1. **Make a Prediction**:
   ```http
   POST /api/datasets/{dataset_id}/problem-details/{problem_detail_id}/best-models/{best_model_id}/predict
   ```
   Include the input data in the request:
   ```json
   {
     "data": {
       "feature1": "value1",
       "feature2": "value2",
       "feature3": "value3"
     }
   }
   ```

2. **Prediction Response**:
   ```json
   {
     "data": {
       "predicted_value": "prediction_result",
       "target_column": "target_column_name"
     }
   }
   ```

## API Documentation
This project uses [Scramble](https://github.com/dedoc/scramble) for API documentation. Scramble automatically generates OpenAPI documentation from your Laravel codebase, making it easy to understand and interact with the API endpoints.

API endpoints are organized into groups using Scramble's attributes, making the documentation clear and well-structured.

Once the Laravel service is running, the documentation is available at:
   ```http
   GET /docs/api#/
   ```
## Security
- All API endpoints are protected with token-based authentication
- Communication between Laravel and Flask services is secured with API tokens
- Dataset access is controlled through user permissions
- Model access is restricted to authorized users

## License
This project is licensed under the MIT License - see the LICENSE file for details.
