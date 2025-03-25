# Banking Application API

## Overview

The **Banking Application API** is a robust backend system developed using Laravel, designed to facilitate banking operations and services. This API provides a seamless interface for managing user accounts, transactions, and financial data.

## Features

-   **User Authentication**: Secure sign-up and login processes, with role-based access control.
-   **Account Management**: Users can create, update, and delete bank accounts, as well as view account details.
-   **Transaction Handling**: Supports various transaction types, including deposits, withdrawals, and transfers.
-   **Data Security**: Implements best practices for data protection, including encryption and secure data storage.
-   **Scalability**: Built with scalability in mind, allowing for future enhancements and increased user load.

## Tech Stack

-   **Framework**: Laravel
-   **Database**: MySQL
-   **Version Control**: Git

## URL

The application can be accessed at: [https://bankingapplicationapi-production.up.railway.app/](https://bankingapplicationapi-production.up.railway.app/)

## API Endpoints

-   **User Registration: [POST]** /api/auth/register
-   **User Login: [POST]** /api/auth/login
-   **Setup PIN: [POST]** /api/onboarding/setup/pin
-   **Create Account Number: [POST]** api/onboarding/generate/account_number
-   **Make a Deposit: [POST]** /api/account/deposit
-   **Make a Withdraw: [POST]** /api/account/withdraw
-   **Make a Transfer: [POST]** /api/account/transfer
-   **Transaction History: [GET]** /api/transaction/history
