# A Rule Engine

Following is an Event Driven Application. Being Static Code Analyser it has purpose to notify through Emails or Slack channel. These alerts are regarding identified vulnerabilities in the config files of End User Application. Given APIs are for the purpose of Token generation, File Uploads, File Scans and Scan Status. https://debricked.com/api open source APIs are consumed to do the operation.

## Table of Contents

- [Installation](#installation)
  - [Docker Setup](#docker-setup)
- [Configuration](#configuration)
  - [Environment Variables](#environment-variables)
- [Usage](#usage)
  - [Notifications](#notifications)
- [Technology Stack](#technology-stack)

## Installation

### Docker Setup

This project is containerized using Docker. To get started, ensure you have Docker and Docker Compose installed on your machine.

1. **Clone the repository:**
   ```bash
   git clone https://github.com/vrj93/the-rule-engine.git
   cd the-rule-engine
   ```

2. **Copy the example environment file and modify as needed:**
   ```bash
   cp .env.example .env
   ```

3. **Build and start the containers:**
   ```bash
   docker-compose up -d --build
   ```

Your application should now be running and accessible at `http://localhost:8080`.

### Docker Commands

- **Start containers:** `docker-compose up -d`
- **Stop containers:** `docker-compose down`
- **Rebuild containers:** `docker-compose up -d --build`
- **Run Artisan commands:** `docker-compose exec app php artisan [command]`

### RabbitMQ: Create Queue

- **Command:** `docker exec -it rule_engine_app php artisan rabbitmq:queue-declare <queue name>`
- **Name** `default, email, slack`

## Configuration

### Environment Variables

Ensure that you configure your `.env` file with the correct details:

- **APP_ENV**: Set to `local` for development or `production` for live environments.
- **DB_CONNECTION**: Ensure this is set to `mysql`.
- **MAIL**: Configure your mail driver, host, port, etc.
- **SLACK_WEBHOOK_URL**: Set your Slack webhook URL for notifications.

Refer to `.env.example` for a complete list of environment variables.

## Usage

### Notifications

This application uses email and Slack notifications for specific alerts:

- **Mail Notifications**: Configured via the `MAIL_*` environment variables. Ensure these are set up correctly for your environment.
- **Slack Notifications**: Set up via the `SLACK_WEBHOOK_URL` in your `.env` file. Alerts will be sent to the specified Slack channel for critical events.

## CI/CD

- Both integration and deployment are automated for the `main` branch.
- GitHub Action workflow is used for the Ops.
- Docker image build, SSH into production, Pulling the latest image from the hub and container building are handled through this workflow.
- GitHub Action `repository secrets` are utilised for sensitive information.

## Technology Stack

- **Language**: PHP 8.2
- **Framework**: Laravel 11
- **Database**: MySQL 8.0
- **Queue Management**: RabbitMQ 3.13.7
- **Containerization**: Docker, Docker Compose
- **Notifications**: Email (via SMTP), Slack Webhook
- **CI/CD**: GitHub Action Workflow
