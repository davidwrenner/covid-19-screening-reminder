from twilio.rest import Client
import re
from env import \
    TWILIO_AUTH_TOKEN, \
    TWILIO_ACCT_SID, \
    NUMBERS_FP, \
    LOG_FP, \
    SERVER_NUMBER

number_re = re.compile(r"^\+[\d]{11}$")
twilio = Client(TWILIO_ACCT_SID, TWILIO_AUTH_TOKEN)
message_content = \
    """
    Good morning! Remember to complete
    your daily Covid-19 screening:
    screening.wustl.edu 
    """


def send_message(target_number, message_body):
    msg = twilio.messages.create(
        body=message_body,
        from_=SERVER_NUMBER,
        to=target_number
    )
    if msg.error_code is not None:
        with open(LOG_FP, "a") as f:
            f.write(
                f"""
                ERROR: {msg.error_code}
                Time: {msg.date_sent} 
                Status: {msg.status}\n
                """
            )


def main():
    with open(NUMBERS_FP, "r") as f:
        contents = f.readlines()
    for number in contents:
        if number and number_re.match(number):
            send_message(number, message_content)


if __name__ == "__main__":
    main()
