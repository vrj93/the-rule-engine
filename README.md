# A Rule Engine

Given APIs are for the purpose of Login(Token generation), File Uploads, File Scans and Upload Status. https://debricked.com/api open source APIs are consumed to do the operation such as scanning and finding different level of vulnerabilities in the Application Config files.

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

## Technology Stack

- **Framework**: Laravel 11
- **Language**: PHP 8.2
- **Database**: MySQL 8
- **Containerization**: Docker, Docker Compose
- **Notifications**: Email (via SMTP), Slack Webhook
