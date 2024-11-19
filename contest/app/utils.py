from fastapi import HTTPException
from decouple import config

ALLOWED_DOMAINS = config("ALLOWED_DOMAINS").split(",")

def normalize_email(email: str) -> str:
    """
    Normalizuje adres email:
    - Konwertuje na małe litery
    - Usuwa kropki i aliasy dla Gmaila
    """
    if "@" not in email:
        raise ValueError("Invalid email format")
    email = email.strip().lower()
    local_part, domain = email.split("@")
    
    if domain in ["gmail.com", "googlemail.com"]:
        local_part = local_part.split("+")[0]
        local_part = local_part.replace(".", "")
    
    return f"{local_part}@{domain}"


def validate_email_domain(email: str):
    """
    Weryfikuje, czy domena email znajduje się na liście dozwolonych.
    """
    domain = email.split("@")[-1]
    if domain not in ALLOWED_DOMAINS:
        raise HTTPException(status_code=400, detail="Invalid email domain")