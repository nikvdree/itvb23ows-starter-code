FROM python:3.10-alpine

WORKDIR /app
COPY requirements.txt /app

RUN pip install -r requirements.txt

COPY . /app

ENV FLASK_RUN_PORT=7000

EXPOSE 7000

CMD ["flask", "run"]