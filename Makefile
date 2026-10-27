# The production WordPress image. The ECR repository and the wordpress
# namespace come from the 2024 setup on main; check both with ops before the
# first deploy (docker/README.md).
REG  := 539779309746.dkr.ecr.us-west-2.amazonaws.com/wordpress
HASH := $(shell git rev-parse --short HEAD)
IMG  := $(REG):$(HASH)
NAMESPACE ?= wordpress

.PHONY: build push deploy

build:
	buildah bud -t $(IMG) .

push:
	aws ecr get-login-password --region us-west-2 | buildah login -u AWS --password-stdin $(REG)
	buildah push $(IMG)

deploy:
	kubectl set image -n $(NAMESPACE) deployment/wordpress wordpress=$(IMG)
