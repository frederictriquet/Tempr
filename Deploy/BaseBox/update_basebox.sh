vagrant box outdated

vagrant destroy -f
vagrant box remove -f BaseBox
rm -rf /home/rodger/VirtualBox\ VMs/BaseBox_basebox*
rm -f BaseBox.box
vagrant up --provision
# ça a créé
# - ~/VirtualBox VMs/BaseBox_basebox_1443729875502_56996


vagrant package --output BaseBox.box
# ça a créé
# - BaseBox.box

vagrant box add BaseBox BaseBox.box
# ça a créé
# - ~/.vagrant.d/boxes/BaseBox

