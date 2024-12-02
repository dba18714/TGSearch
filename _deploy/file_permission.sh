#!/bin/bash

if [ -f "/etc/init.d/bt" ]; then
  chown -R www $(pwd);
fi
