# Corporate Vehicle Licensing Platform

Laravel 11.31 application for corporate fleet licensing in Zimbabwe.

## Main Features

- Corporate registration and admin approval
- Vehicle CRUD and CSV import
- Auto-generated licensing quotes
- Wallet, EcoCash, and CBZ Direct payment flows
- PDF license disks with QR verification
- Public vehicle licensing bot at `/vehicle-licensing`
- Credit applications with KYC approval
- Bike delivery orders and status tracking
- Admin pricing and payment views

## Vehicle Bot Flow

The customer starts at `/vehicle-licensing`, selects:

1. Buy License
2. Buy License on Credit
3. View Vehicle Details

The customer enters only the number plate first. The app looks up the vehicle, generates a quote, and shows fees for ZINARA, radio license, insurance, arrears, delivery, and total.

## Demo Data

Run migrations and seeders:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Seeded admin:

```text
admin@example.com
password
```

Seeded demo vehicle number plate:

```text
ABC1234
```

## Local Development

```bash
npm install
npm run build
php artisan serve
```

Tests were intentionally not run during this implementation pass.
