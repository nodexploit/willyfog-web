#!/usr/bin/env bash

# Install docker on the virtual machine

DOCKER_COMPOSE_VERSION=1.7.1

if [ "$EUID" -ne 0  ]
then
    echo "Please run as root"
    exit
fi

if [ -z "$1"  ]
then
    ROOT_USER=vagrant
else
    ROOT_USER=$1
fi

# Utilities
apt-get -y install git curl

# Docker installation
# https://docs.docker.com/engine/installation/linux/ubuntulinux/
apt-get update
apt-get -y install apt-transport-https ca-certificates
apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D

echo "deb https://apt.dockerproject.org/repo ubuntu-trusty main" >  /etc/apt/sources.list.d/docker.list

apt-get update
apt-get -y purge lxc-docker
apt-cache policy docker-engine

# Prerequisites Ubuntu 14.04
apt-get update
apt-get -y install linux-image-extra-$(uname -r)
apt-get -y install apparmor

# Install docker
apt-get update
apt-get -y install docker-engine
service docker start

# Create docker group
groupadd docker
usermod -aG docker ${ROOT_USER}

# Upgrade docker
#apt-get -y upgrade docker-engine

# Install docker compose
curl -L https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Bash completion for docker-compose
curl -L https://raw.githubusercontent.com/docker/compose/$(docker-compose version --short)/contrib/completion/bash/docker-compose > /etc/bash_completion.d/docker-compose