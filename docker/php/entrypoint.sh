#!/bin/sh

make install
symfony server:stop
symfony serve