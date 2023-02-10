#!/bin/sh

make reinstall
symfony server:stop
symfony serve