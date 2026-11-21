import { test, expect } from '@playwright/test';

test('home renders both wallet QR codes inside the demo platform shell', async ({ page }) => {
  await page.goto('/');
  await expect(page.getByTestId('demo-platform')).toBeVisible();
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Wallet QR Bundle Demo');
  await expect(page.getByAltText('Google Wallet QR')).toBeVisible();
  await expect(page.getByAltText('Apple Wallet QR')).toBeVisible();
});
