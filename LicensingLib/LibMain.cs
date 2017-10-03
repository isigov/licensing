using System;
using System.Collections.Generic;
using System.Text;
using System.Net;
using Microsoft.VisualBasic;
using System.Management;
using System.IO;
using System.Windows.Forms;

namespace LicensingLib
{
    public class returnMsg
    {
        public static string disabled = "User CP login is currently disabled, please contact the administrator.";
        public static string uninstalled = "Please run the installer";
        public static string success = "You've logged in successfully!";
        public static string unsuccessful = "Login was unsuccessful, please check your username and password.";
        public static string banned = "You have been banned for:";
        public static string approve = "Your account must be approved by an Administrator before you can login!";
        public static string nocreate = "Creation of new accounts is currently prohibited!";
        public static string cantcreate = "Cannot add user ";
    }
    public class LibMain
    {
        public string AppName;
        public string WebPath;
        public string HWID;
        public bool LoginSuccess;
        public string News;
        public string BanReason;
        public string LoginReason;
        public bool Banned;
        public bool ImpersonationServer;
        public string UpdateMsg;
        public bool UpdateOptional;
        public string UpdateLink;
        public string UpdateVersion;
        public string UpdateDate;
        public CookieContainer CheckLogin(string User, string Pass)
        {
            if (AppName == null)
                throw new Exception("AppName not initalized");
            if (WebPath == null)
                throw new Exception("WebPath not initalized");

            HWID = getUniqueID("C");

            byte[] requestData = Encoding.ASCII.GetBytes("action=login&type=applogin&user=" + User + "&pass=" + Pass + "&hwid=" + HWID + "&app=" + AppName);
            HttpWebRequest request = (HttpWebRequest)HttpWebRequest.Create(WebPath + "login.php");
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";
            request.CookieContainer = new CookieContainer();
            request.UserAgent = "Licensing Application v1.00";
            request.ContentLength = requestData.Length;
            Stream writeStream = request.GetRequestStream();
            writeStream.Write(requestData, 0, requestData.Length);
            writeStream.Close();

            HttpWebResponse response = (HttpWebResponse)request.GetResponse();
            if (response.StatusCode != HttpStatusCode.OK)
            {
                LoginReason = "Cannot properly access server!";
                LoginSuccess = false;
                return null;
            }
            else
            {
                string strResponse = new StreamReader(response.GetResponseStream()).ReadToEnd();
                response.Close();
                if (strResponse.Contains(returnMsg.disabled))
                {
                    LoginReason = "The Administrator has disabled application logins!";
                    LoginSuccess = false;
                }
                if (strResponse.Contains(returnMsg.uninstalled))
                {
                    LoginReason = "The Licensing Panel has not been properly installed yet!";
                    LoginSuccess = false;
                }
                if (strResponse.Contains(returnMsg.unsuccessful))
                {
                    LoginReason = "Invalid Username or Password.";
                    LoginSuccess = false;
                }
                if (strResponse.Contains(returnMsg.banned))
                {
                    LoginSuccess = false;
                    Banned = true;
                    BanReason = Strings.Split(Strings.Split(strResponse, "</FONT></DIV>", -1, CompareMethod.Text)[0], returnMsg.banned, -1, CompareMethod.Text)[1];
                }
                if (strResponse.Contains(returnMsg.approve))
                {
                    LoginReason = "Your account must be approved by an administrator before you can login!";
                    LoginSuccess = false;
                }
                if (strResponse.Contains(returnMsg.success))
                {
                    if (request.CookieContainer.Count != 1)
                    {
                        LoginReason = "Impersonation server detected!";
                        LoginSuccess = false;
                        ImpersonationServer = true;
                        return null;
                    }
                    LoginSuccess = true;
                    CookieContainer container = request.CookieContainer;
                    request = (HttpWebRequest)HttpWebRequest.Create(WebPath + "login.php");
                    request.Method = "GET";
                    request.ContentType = "application/x-www-form-urlencoded";
                    request.CookieContainer = container;
                    request.UserAgent = "Licensing Application v1.00";
                    response = (HttpWebResponse)request.GetResponse();
                    strResponse = new StreamReader(response.GetResponseStream()).ReadToEnd();
                    News = Strings.Split(Strings.Split(strResponse, "<tr><td>News:</td><td width=\"500\">", -1, CompareMethod.Text)[1], "</b></DIV><br></td>", -1, CompareMethod.Text)[0].Replace("<br />", "\r\n").Replace("<br>", "\r\n").Replace("<DIV ALIGN=RIGHT><b>", "");
                    response.Close();
                    return request.CookieContainer;
                }
                return null;
            }
        }
        public void GetUpdates(CookieContainer cookie)
        {
            if (AppName == null)
                throw new Exception("AppName not initalized");
            if (WebPath == null)
                throw new Exception("WebPath not initalized");

            byte[] requestData = Encoding.ASCII.GetBytes("viewversion=" + AppName);
            HttpWebRequest request = (HttpWebRequest)HttpWebRequest.Create(WebPath + "login.php");
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";
            request.CookieContainer = cookie;
            request.UserAgent = "Licensing Application v1.00";
            request.ContentLength = requestData.Length;
            Stream writeStream = request.GetRequestStream();
            writeStream.Write(requestData, 0, requestData.Length);
            writeStream.Close();

            HttpWebResponse response = (HttpWebResponse)request.GetResponse();
            if (response.StatusCode != HttpStatusCode.OK)
            {
                LoginReason = "Cannot properly access server!";
                LoginSuccess = false;
                return;
            }
            else
            {
                string strResponse = new StreamReader(response.GetResponseStream()).ReadToEnd();
                response.Close();
                UpdateDate = Strings.Split(Strings.Split(strResponse, "<tr><td>", -1, CompareMethod.Text)[2], "</td><td>", -1, CompareMethod.Text)[0];
                UpdateVersion = Strings.Split(Strings.Split(strResponse, "<tr><td>", -1, CompareMethod.Text)[2], "</td><td>", -1, CompareMethod.Text)[1];
                UpdateLink = Strings.Split(Strings.Split(Strings.Split(Strings.Split(strResponse, "<tr><td>", -1, CompareMethod.Text)[2], "</td><td>", -1, CompareMethod.Text)[2], "<a href=", -1, CompareMethod.Text)[1], ">", -1, CompareMethod.Text)[0];
                UpdateMsg = Strings.Split(Strings.Split(strResponse, "<tr><td>", -1, CompareMethod.Text)[2], "</td><td>", -1, CompareMethod.Text)[3].Replace("<br />", "");
                
            }
        }
        public void CreateAccount(string User, string Pass, string Email)
        {
            if (AppName == null)
                throw new Exception("AppName not initalized");
            if (WebPath == null)
                throw new Exception("WebPath not initalized");

            HWID = getUniqueID("C");

            byte[] requestData = Encoding.ASCII.GetBytes("action=make&user=" + User + "&pass=" + Pass + "&hwid=" + HWID + "&email=" + Email + "&app=" + AppName);
            HttpWebRequest request = (HttpWebRequest)HttpWebRequest.Create(WebPath + "login.php");
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";
            request.CookieContainer = new CookieContainer();
            request.UserAgent = "Licensing Application v1.00";
            request.ContentLength = requestData.Length;
            Stream writeStream = request.GetRequestStream();
            writeStream.Write(requestData, 0, requestData.Length);
            writeStream.Close();

            HttpWebResponse response = (HttpWebResponse)request.GetResponse();
            if (response.StatusCode != HttpStatusCode.OK)
            {
                LoginReason = "Cannot properly access server!";
                LoginSuccess = false;
            }
            else
            {
                string strResponse = new StreamReader(response.GetResponseStream()).ReadToEnd();
                response.Close();
                if (strResponse.Contains(returnMsg.nocreate))
                {
                    LoginReason = "The Administrator has disabled the creation of new accounts!";
                    LoginSuccess = false;
                }
                if (strResponse.Contains(returnMsg.cantcreate))
                {
                    LoginReason = "Cannot create new user: User already exists!";
                    LoginSuccess = false;
                }
            }
        }
        private string getUniqueID(string drive)
        {
            if (drive == string.Empty)
            {
                foreach (DriveInfo compDrive in DriveInfo.GetDrives())
                {
                    if (compDrive.IsReady)
                    {
                        drive = compDrive.RootDirectory.ToString();
                        break;
                    }
                }
            }

            if (drive.EndsWith(":\\"))
            {
                drive = drive.Substring(0, drive.Length - 2);
            }

            string volumeSerial = getVolumeSerial(drive);
            string cpuID = getCPUID();

            return cpuID.Substring(13) + cpuID.Substring(1, 4) + volumeSerial + cpuID.Substring(4, 4);
        }

        private string getVolumeSerial(string drive)
        {
            ManagementObject disk = new ManagementObject(@"win32_logicaldisk.deviceid=""" + drive + @":""");
            disk.Get();

            string volumeSerial = disk["VolumeSerialNumber"].ToString();
            disk.Dispose();

            return volumeSerial;
        }

        private string getCPUID()
        {
            string cpuInfo = "";
            ManagementClass managClass = new ManagementClass("win32_processor");
            ManagementObjectCollection managCollec = managClass.GetInstances();

            foreach (ManagementObject managObj in managCollec)
            {
                if (cpuInfo == "")
                {
                    cpuInfo = managObj.Properties["processorID"].Value.ToString();
                    break;
                }
            }

            return cpuInfo;
        }
    }
}
