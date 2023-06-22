FROM python:3-slim
WORKDIR /app
COPY . .
RUN apt-get --assume-yes update && \
    apt-get --assume-yes install curl && \
	apt-get --assume-yes install bash 

RUN pip install --upgrade pip && \
    pip install python-lsp-server
	
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR="/root/.nvm"
ENV SH_VERSION="1.38.0"
RUN . "$NVM_DIR/nvm.sh" && nvm install 18.16.0
RUN . "$NVM_DIR/nvm.sh" && nvm alias default stable

# Add NVM binaries to the system PATH
ENV PATH="/root/.nvm/versions/node/v18.16.0/bin:${PATH}"	

CMD ["node", "server.mjs"]
EXPOSE 3001
EXPOSE 3002