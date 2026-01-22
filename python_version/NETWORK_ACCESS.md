# 📱 Access Trust Mora Bank from Other Devices

## ✅ Network Access Now Enabled!

Your Flask app is now configured to accept connections from other devices on your network.

---

## 🌐 How to Access from Other Devices

### Step 1: Find Your Computer's IP Address

Run this command on your server laptop:
```bash
ipconfig | findstr IPv4
```

You'll see something like:
```
IPv4 Address. . . . . . . . . . . : 192.168.1.105
```

### Step 2: Start Your Flask App

```bash
cd "c:\Users\TIMON\Desktop\Hospital-Management-System-dbmsminiproject-main\hospital system\PROJECT"
python main.py
```

You should see:
```
* Running on all addresses (0.0.0.0)
* Running on http://127.0.0.1:5000
* Running on http://192.168.1.105:5000
```

### Step 3: Access from Any Device

**On any device connected to the SAME WiFi:**

Open a browser and go to:
```
http://YOUR_IP_ADDRESS:5000
```

**Examples:**
- Phone: `http://192.168.1.105:5000`
- Another laptop: `http://192.168.1.105:5000`
- Tablet: `http://192.168.1.105:5000`

---

## ✅ What Was Fixed

1. **Flask Configuration**: Changed to `host='0.0.0.0'` (allows all network connections)
2. **Firewall Rule**: Created "Trust Mora Bank Flask Port 5000" rule to allow incoming traffic
3. **Port**: Port 5000 is now accessible from your local network

---

## 📋 Quick Checklist

Before accessing from other devices:

- [ ] Flask app is running (`python main.py`)
- [ ] You know your IP address (from `ipconfig`)
- [ ] Other device is on the **same WiFi network**
- [ ] Using format: `http://YOUR_IP:5000`

---

## 🔍 Troubleshooting

**Problem: "Can't connect" or "Site can't be reached"**

1. **Verify same network:**
   ```bash
   # On server laptop:
   ipconfig | findstr IPv4
   
   # On other device (if Windows):
   ipconfig | findstr IPv4
   ```
   Both should have similar IPs (e.g., both 192.168.1.X)

2. **Test ping:**
   From other device:
   ```bash
   ping 192.168.1.105
   ```
   Should get replies, not timeouts.

3. **Test on server laptop first:**
   On your laptop browser: `http://192.168.1.105:5000`
   If this works but others can't → check their WiFi connection

4. **Firewall double-check:**
   Run this to verify the rule exists:
   ```powershell
   Get-NetFirewallRule -DisplayName "Trust Mora Bank Flask Port 5000"
   ```

**Problem: "Connection refused"**

- Make sure Flask is actually running
- Check if you see "Running on http://0.0.0.0:5000" in the console

---

## 📱 Mobile Tips

**For the best mobile experience:**

1. **Save as bookmark** on your phone's home screen
2. **Use landscape mode** for better dashboard view
3. **Dark/Light theme** works on mobile too!
4. **Responsive design** adapts to all screen sizes

---

## ⚠️ Important Security Notes

**This setup is for LOCAL NETWORK development only!**

- ✅ Safe on your home/office WiFi
- ❌ Don't expose to the internet
- ❌ Don't use on public WiFi
- ❌ Not for production deployment

For production, you'd need:
- HTTPS encryption
- Proper domain name
- Cloud hosting (AWS, Azure, Heroku, etc.)
- Enhanced security measures

---

## 🎉 You're All Set!

Your Trust Mora Bank app is now accessible from:
- ✅ Your laptop (localhost:5000)
- ✅ Your phone (same WiFi)
- ✅ Other laptops (same WiFi)
- ✅ Tablets (same WiFi)

**Just remember to use YOUR IP address!** 🚀
