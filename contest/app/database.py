from typing import List
from pydantic import BaseModel

# Simulated in-memory database
class Submission(BaseModel):
    email: str
    number: int

submissions: List[Submission] = []
