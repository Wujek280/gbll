#!/usr/bin/env python3
import subprocess
import sys
import re
import signal

def handle_sigint(sig, frame):
    print("\nAborted by user.")
    sys.exit(130)

signal.signal(signal.SIGINT, handle_sigint)

# Optional regex filter from argv[1]
search = sys.argv[1] if len(sys.argv) > 1 else None

# Get branches sorted by committerdate (oldest → newest)
cmd = ["git", "for-each-ref", "--sort=committerdate", "refs/heads/", "--format=%(refname:short)"]
branches = subprocess.check_output(cmd, text=True).splitlines()
branches = [b.strip() for b in branches if b.strip()]

# Filter
if search:
    regex = re.compile(search)
    branches = [b for b in branches if regex.search(b)]

if not branches:
    sys.exit(0)

# Print options so that [1] = newest
n = len(branches)
for idx, name in enumerate(branches):
    disp = n - idx
    print(f"\033[1;34m [{disp}] - {name} \033[0m")

# Read selection
try:
    sel = input("> ").strip()
except EOFError:
    print("Cancelled.")
    sys.exit(0)

if not sel:
    print("Cancelled.")
    sys.exit(0)

try:
    sel = int(sel)
except ValueError:
    print("Invalid selection.")
    sys.exit(1)

idx = n - sel
if idx < 0 or idx >= n:
    print("Invalid selection.")
    sys.exit(1)

branch = branches[idx]
print(f"Switching to {branch}...")
subprocess.call(["git", "checkout", branch])
