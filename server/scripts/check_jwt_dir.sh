#!/bin/bash
if ! [ -d ./config/jwt/ ]; then
  php bin/console lexik:jwt:generate-keypair --no-interaction
else
  php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
fi