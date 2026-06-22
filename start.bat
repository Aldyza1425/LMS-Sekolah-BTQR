@echo off
title BTQR - Starter

echo ============================================
echo   BTQR - Menjalankan Backend dan Frontend
echo ============================================
echo.

:: ── Backend: Laravel ──────────────────────────
echo [1/2] Memulai Backend Laravel (port 8000)...
start "BTQR Backend - Laravel" cmd /k "cd /d %~dp0backend && php artisan serve --host=127.0.0.1 --port=8000"

:: Tunggu sebentar agar backend siap
timeout /t 2 /nobreak > nul

:: ── Frontend: Vue / Vite ──────────────────────
echo [2/2] Memulai Frontend Vue/Vite (port 5173)...
start "BTQR Frontend - Vite" cmd /k "cd /d %~dp0frontend && npm run dev"

echo.
echo ============================================
echo   Kedua server berhasil dijalankan!
echo   Backend  : http://127.0.0.1:8000
echo   Frontend : http://localhost:5173
echo ============================================
echo.
echo Tutup jendela ini atau tekan tombol apa saja...
pause > nul
