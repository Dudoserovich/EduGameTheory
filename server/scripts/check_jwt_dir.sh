#!/bin/bash
if ! [ -d ./config/jwt/ ]; then
  php bin/console lexik:jwt:generate-keypair
else
  php bin/console lexik:jwt:generate-keypair --overwrite -n
fi