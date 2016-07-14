# -*- mode: ruby -*-
# vi: set ft=ruby :

#################################################################################################
#                                                                                               #
# Please read before `vagrant up`!                                                              #
#                                                                                               #
# Configuration                                                                                 #
# ---------------------                                                                         #
# PUBLIC_GUEST_IP: If you set this, this IP will be used by your machine in your local network. #
# To set it execute:                                                                            #
#       `echo 'export PUBLIC_GUEST_IP=<desiredIP>' >> ~/.bashrc`                                #
#                                                                                               #
# PUBLIC_GUEST_IP: You have to set this to use the debugger correctly.                          #                                                                           #
#       `echo 'export PUBLIC_HOST_IP=<desiredIP>' >> ~/.bashrc`                                 #
#                                                                                               #
# PRIVATE_GUEST_IP: This IP will always be used by your machine in your private network.        #
#                                                                                               #
# PROJECTS: enumerated list of the different projects under `projects\` directory. It's         #
# necessary because of the permissions.                                                         #
#                                                                                               #
#################################################################################################

# Constants
# ---------------------
PUBLIC_GUEST_IP = "#{ENV['PUBLIC_GUEST_IP']}"
PUBLIC_HOST_IP = "#{ENV['PUBLIC_HOST_IP']}"
PRIVATE_GUEST_IP = '192.168.33.10'

Vagrant.configure(2) do |config|
  # Base Box
  # --------------------
  config.vm.box = "ubuntu/trusty64"

  # Private network
  # --------------------
  config.vm.network "private_network", ip: PRIVATE_GUEST_IP

  # Public network
  # --------------------
  #if PUBLIC_GUEST_IP
  #  config.vm.network "public_network", ip: PUBLIC_GUEST_IP
  #end

  # Optional (Remove if desired)
  config.vm.provider :virtualbox do |vb|
    # How much RAM to give the VM (in MB)
    # -----------------------------------
    vb.memory = "2048"
  end

  # Provisioning Script
  # --------------------
  config.vm.provision "shell", path: "bootstrap/install-docker.sh"
  config.vm.provision "shell", path: "bootstrap/provision.sh", args: [File.basename(Dir.getwd), PUBLIC_HOST_IP]

  # Synced Folder
  # --------------------
  config.vm.synced_folder ".", "/home/vagrant/#{File.basename(Dir.getwd)}"
  config.vm.synced_folder ".", "/var/www/#{File.basename(Dir.getwd)}", mount_options: [ "dmode=774", "fmode=664" ], owner: 'vagrant', group: 'www-data'
end
