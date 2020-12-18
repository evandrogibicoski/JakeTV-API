# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "williamyeh/ubuntu-trusty64-docker"

  config.vm.network "private_network", ip: "192.168.33.11"
  config.vm.hostname = "local.jaketv.tv"

  config.vm.provider "virtualbox" do |vb|
     vb.customize ["modifyvm", :id, "--memory", "1024"]
  end

  config.vm.synced_folder ".", "/home/vagrant/app", create: true

  config.vm.provision :shell, inline: "docker build -t jaketvweb --build-arg ENV=local ./app"

  config.vm.provision :shell, run: "always", inline: %{
    docker rm -f web 2>/dev/null
    docker run -d -v /home/vagrant/app/composer.json:/share/composer.json -v /home/vagrant/app/composer.lock:/share/composer.lock -v /home/vagrant/app/vendor:/share/vendor -v /home/vagrant/app/resources:/share/resources -v /home/vagrant/app/app:/share/app -v /home/vagrant/app/config:/share/config -v /home/vagrant/app/database:/share/database -v /home/vagrant/app/public:/share/public -v /home/vagrant/app/routes:/share/routes -e DB_HOST=jaketv-dev.chagf3dwtgl1.us-west-2.rds.amazonaws.com -e=APP_ENV=local -e=APP_URL=http://local.jaketv.tv -e APP_DEBUG=true -e=APP_KEY=XbhnGhlN8O0cGReNiodsMqBQ165HO5rp -e DB_DATABASE=jaketvdev -e DB_USER=jaketvTest -e DB_PASS=jaketv100 -p 80:80 --name web jaketvweb
  }

  #v /home/vagrant/app:/share
end
