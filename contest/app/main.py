from fastapi import FastAPI, HTTPException
from fastapi.staticfiles import StaticFiles
from fastapi.responses import FileResponse
from app.models import SubmissionRequest
from app.database import submissions, Submission
from app.utils import normalize_email, validate_email_domain
from pydantic import ValidationError

app = FastAPI()

# Montowanie folderu static
app.mount("/static", StaticFiles(directory="static"), name="static")

# Endpoint główny dla `index.html`
@app.get("/")
def serve_homepage():
    return FileResponse("static/index.html")


@app.post("/submit/")
def submit_entry(entry: SubmissionRequest):
    try:
        # Explicitly validate the input
        print("Received data:", entry.model_dump())  # Use model_dump() instead of dict()
        
        normalized_email = normalize_email(entry.email)
        validate_email_domain(normalized_email)
        
        if any(sub.email == normalized_email for sub in submissions):
            raise HTTPException(status_code=400, detail="Duplicate entry")

        new_entry = Submission(email=normalized_email, number=entry.number)
        submissions.append(new_entry)
        return {"message": "Entry submitted successfully"}
    
    except ValidationError as e:
        raise HTTPException(status_code=422, detail=str(e))
    
@app.get("/winner/")
def pick_winner():
    if not submissions:
        raise HTTPException(status_code=400, detail="No submissions available")
    import random
    winner = random.choice(submissions)
    return {"winner": winner.email, "number": winner.number}
