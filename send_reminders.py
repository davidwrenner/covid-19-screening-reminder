from twilio.rest import Client
from datetime import datetime
import re
from env import \
    TWILIO_AUTH_TOKEN, \
    TWILIO_ACCT_SID, \
    NUMBERS_FP, \
    LOG_FP, \
    SERVER_NUMBER

# regex to parse something of this form:
# +11234567890,00:00,0100000
# number,hour:minute,days
line_parse_re = re.compile(r"^(?P<number>[+\d]{12}),(?P<hour>[\d]{2}):(?P<minute>[\d]{2}),(?P<days>[\d]{7})$")
twilio = Client(TWILIO_ACCT_SID, TWILIO_AUTH_TOKEN)
message_content = """
Good morning! Remember to complete your daily Covid-19 screening:
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
    current_dt = datetime.now()
    current_hour = current_dt.hour
    current_minute = current_dt.minute
    current_weekday = current_dt.weekday()

    with open(NUMBERS_FP, "r") as f:
        contents = f.readlines()

    for line in contents:
        if not line or not line_parse_re.match(line):
            continue

        m = line_parse_re.match(line)
        number = m.group("number")
        hour = 0 if m.group("hour") == "00" else int(m.group("hour").lstrip("0"))
        minute = 0 if m.group("minute") == "00" else int(m.group("minute").lstrip("0"))
        days = m.group("days")

        with open(LOG_FP, "a") as f:
            f.write(
                f"""
                day_bool: {bool(int(days[current_weekday]))}
                hour_bool: {current_hour == hour} 
                min_bool: {current_minute == minute}\n
                day: {current_weekday}, {days[current_weekday]}
                hour: {current_hour}, {hour}
                min: {current_minute}, {minute}
                """
            )

        if bool(int(days[current_weekday])) \
                and current_hour == hour \
                and current_minute == minute:
            send_message(number, message_content)


if __name__ == "__main__":
    main()
