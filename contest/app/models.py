from pydantic import BaseModel, EmailStr, Field

class SubmissionRequest(BaseModel):
    email: EmailStr
    number: int = Field(..., ge=0, le=9, description="A single-digit number")
