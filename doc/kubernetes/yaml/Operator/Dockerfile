FROM python:3.7
COPY handlers.py /handlers.py
RUN pip install kopf kubernetes
CMD kopf run --standalone /handlers.py
