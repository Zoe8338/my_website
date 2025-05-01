# My Self-Hosting Journey Begins – Day 1 (April 25th, 2025)

Today marks the beginning of my self-hosting and networking learning journey — and I’m thrilled to share how it started!

## Installing Proxmox VE

I started off by installing **Proxmox VE** on an old laptop. Once it was up and running, I connected an external hard drive via USB, mounted it into the system, and added it to Proxmox as additional storage.

With that in place, I created a virtual machine and set its disk to live on the external drive. I then installed **Ubuntu Server** on that VM.

## Secure External Access via Tunnel

Since I’m on a mobile data connection that doesn’t support port forwarding, I had to get creative with remote access.

Using a **Pangolin** VPS I already had running in the cloud, I created a secure tunnel from the VPS to my Proxmox virtual machine. This setup allows me to access my homelab remotely — without exposing any ports to the public internet. It’s secure, convenient, and perfect for my mobile connection limitations.

## Services I Set Up

With the core infrastructure in place, I started spinning up some useful services:

- **Dockage** – A UI to manage my Docker containers easily.
- **Paperless NGX** – To digitize and organize all my PDF documents.
- **Snipit** – A central place where I’m logging information about my devices and consumables, like printer ink. This way I can keep track of everything I buy in one place.
- **Ghost Blog** – Yep, the very blog you’re reading right now! This is where I’ll be documenting my journey and sharing the things I learn along the way.

## All Secured with HTTPS

Everything is now externally accessible through the tunnel and secured with **HTTPS** — which gives me peace of mind as I explore more advanced setups in the future.
