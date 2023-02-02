#!/bin/bash
export VM_ENV=dev
cd /opt/Tempr/Deploy/BaseBox && ./update_basebox.sh && cd /opt/Tempr/Deploy/Vagrant && ./dev_scratch.sh
