# Docker Desktop Troubleshooting

## Error: "request returned 500 Internal Server Error for API route"

This error indicates Docker Desktop is not running properly or there's an API version mismatch.

### Solution Steps:

1. **Restart Docker Desktop**
   - Close Docker Desktop completely
   - Wait a few seconds
   - Restart Docker Desktop
   - Wait until it fully starts (whale icon should be steady, not animating)

2. **Check Docker Desktop Status**
   - Open Docker Desktop
   - Check if it shows "Docker Desktop is running"
   - Look for any error messages in the Docker Desktop UI

3. **Reset Docker Desktop (if restart doesn't work)**
   - Open Docker Desktop
   - Go to Settings → Troubleshoot
   - Click "Clean / Purge data" or "Reset to factory defaults"
   - Restart Docker Desktop

4. **Check Windows WSL2 (if using WSL2 backend)**
   ```powershell
   wsl --list --verbose
   wsl --shutdown
   ```
   Then restart Docker Desktop

5. **Update Docker Desktop**
   - Make sure you're using the latest version of Docker Desktop
   - Check for updates in Docker Desktop → Settings → General

6. **Check Docker Service**
   - Open Services (services.msc)
   - Find "Docker Desktop Service"
   - Ensure it's running
   - If not, start it manually

7. **Try Alternative: Use Docker without Desktop**
   If Docker Desktop continues to have issues, you can try:
   - Use Docker Engine directly (Linux)
   - Or use an alternative like Podman

### After Fixing Docker Desktop:

Once Docker Desktop is running properly, try again:

```bash
docker ps
docker pull nginx:alpine
docker-compose up -d --build
```


