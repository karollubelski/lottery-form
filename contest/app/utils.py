import requests
from fastapi import HTTPException
from decouple import config

ALLOWED_DOMAINS = config("ALLOWED_DOMAINS").split(",")

def normalize_email(email: str) -> str:
    """
    Normalizuje adres email:
    - Konwertuje na małe litery
    - Usuwa kropki i aliasy dla Gmaila
    """
    email = email.strip().lower()
    local_part, domain = email.split("@")
    
    if domain in ["gmail.com", "googlemail.com"]:
        local_part = local_part.split("+")[0]  # Usuń aliasy
        local_part = local_part.replace(".", "")  # Usuń kropki
    
    return f"{local_part}@{domain}"

def validate_email_domain(email: str):
    """
    Weryfikuje, czy domena email znajduje się na liście dozwolonych.
    """
    domain = email.split("@")[-1]
    if domain not in ALLOWED_DOMAINS:
        raise HTTPException(status_code=400, detail="Invalid email domain")

def verify_captcha(token: str):
    """
    Weryfikuje token CAPTCHA za pomocą API reCAPTCHA.
    """
    secret = config("CAPTCHA_SECRET")
    url = "https://www.google.com/recaptcha/api/siteverify"
    payload = {"secret": secret, "response": token}
    response = requests.post(url, data=payload)
    result = response.json()
    
    if not result.get("success", False):
        raise HTTPException(status_code=400, detail="Invalid CAPTCHA")
