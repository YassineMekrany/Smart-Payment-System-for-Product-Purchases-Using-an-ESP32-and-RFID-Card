#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ThingSpeak.h>
#include <ArduinoJson.h>

#define SS_PIN 5  
#define RST_PIN 4  
MFRC522 mfrc522(SS_PIN, RST_PIN);  
#define ON_Board_LED 2  
const char* ssid = "android@";
const char* password = "1234567891012";
unsigned long myChannelNumber = 2575207;
const char* apiKey = "DFVD412632QINSQQ";

const char* server = "192.168.43.50:8080/Paiement_Intelligent";

WiFiClient client;

int readsuccess;
byte readcard[4];
char str[32] = "";
String StrUID;

bool newScan = false;

void setup() {
  Serial.begin(115200);  
  SPI.begin();  
  mfrc522.PCD_Init();  

  delay(500);

  WiFi.begin(ssid, password);  
  Serial.println("");

  pinMode(ON_Board_LED, OUTPUT); 
  digitalWrite(ON_Board_LED, HIGH);  

  Serial.print("Connecting");
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    digitalWrite(ON_Board_LED, LOW);
    delay(250);
    digitalWrite(ON_Board_LED, HIGH);
    delay(250);
  }
  digitalWrite(ON_Board_LED, HIGH);  
  Serial.println("");
  Serial.print("Successfully connected to : ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  Serial.println("Please tag a card or keychain to see the UID !");
  Serial.println("");

  ThingSpeak.begin(client);
}

void loop() {
  readsuccess = getid();

  if (readsuccess) {
    newScan = true;  

    digitalWrite(ON_Board_LED, LOW);
    HTTPClient http;  

    String UIDresultSend, postData;
    UIDresultSend = StrUID;

    postData = "UIDresult=" + UIDresultSend;

    http.begin("http://192.168.43.50:8080/Paiement_Intelligent/getUID.php");  
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");  

    int httpCode = http.POST(postData);  
    String payload = http.getString();  

    Serial.println(UIDresultSend);
    Serial.println(httpCode);  
    Serial.println(payload);  

    http.end();  
    delay(1000);
    digitalWrite(ON_Board_LED, HIGH);
  }

  if (newScan && WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    String url = String("http://") + server + "/fetch_transactions.php";
    http.begin(url);

    int httpCode = http.GET();
    if (httpCode > 0) {
      int total_achats = 0;  

      String payload = http.getString();
      Serial.println(payload);

      DynamicJsonDocument doc(1024);
      deserializeJson(doc, payload);
      String nam_achats = "";
      String nbre_achats_str = ""; 
      for (JsonObject transaction : doc.as<JsonArray>()) {
        String product_name = transaction["product_name"];
        int nbre_achats_int = transaction["nbre_achats"]; 
        total_achats += nbre_achats_int;         
        nam_achats += String(product_name) + "|";
        nbre_achats_str += String(nbre_achats_int) + "|"; 
        
        Serial.print("Product: ");
        Serial.print(product_name);
        Serial.print(" - Purchases: ");
        Serial.println(nbre_achats_int);
      }

      Serial.print("Total Achats: ");
      Serial.println(total_achats);
      ThingSpeak.setField(1,nam_achats);
      ThingSpeak.setField(2,nbre_achats_str); 
      ThingSpeak.setField(3,total_achats);

      int x = ThingSpeak.writeFields(myChannelNumber, apiKey);
      if (x == 200) {
        Serial.println("Channel update successful.");
      } else {
        Serial.println("Problem updating channel. HTTP error code " + String(x));
      }
    } else {
      Serial.println("Error on HTTP request");
    }

    http.end();

    newScan = false;  
  }

  delay(1000); 
}

int getid() {  
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return 0;
  }
  if (!mfrc522.PICC_ReadCardSerial()) {
    return 0;
  }

  Serial.print("THE UID OF THE SCANNED CARD IS : ");
  
  for (int i = 0; i < 4; i++) {
    readcard[i] = mfrc522.uid.uidByte[i];  
    array_to_string(readcard, 4, str);
    StrUID = str;
  }
  mfrc522.PICC_HaltA();
  return 1;
}

void array_to_string(byte array[], unsigned int len, char buffer[]) {
  for (unsigned int i = 0; i < len; i++) {
    byte nib1 = (array[i] >> 4) & 0x0F;
    byte nib2 = (array[i] >> 0) & 0x0F;
    buffer[i*2+0] = nib1 < 0xA ? '0' + nib1 : 'A' + nib1 - 0xA;
    buffer[i*2+1] = nib2 < 0xA ? '0' + nib2 : 'A' + nib2 - 0xA;
  }
  buffer[len*2] = '\0';
}
