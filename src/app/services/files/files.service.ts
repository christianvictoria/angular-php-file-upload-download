import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders  } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FilesService {

  BASE_URL = "http://localhost/angular-file-upload/backend-rest-api/";
  constructor(
  	private http: HttpClient
  ) { }

  public createFileRequest = async (endpoint: string, formData: FormData): Promise<any> => {
  	const response: any = await this.http.post(this.BASE_URL + btoa(endpoint), formData).toPromise();
  	return response;
  }

  public sendApiRequest = async (endpoint: string, data: any): Promise<any> => {
  	const response: any = await this.http.post(this.BASE_URL + btoa(endpoint), btoa(JSON.stringify(data))).toPromise();
  	return response;
  }

  public downloadFileRequest = async (endpoint: string, data: any): Promise<any> => {
    const response: any = await this.http.post(this.BASE_URL + btoa(endpoint), btoa(JSON.stringify(data)), {responseType: 'blob'}).toPromise();
    return response;
  }

  public watermarkedFileRequest = async (endpoint: string, data: any): Promise<any> => {
    const response: any = await this.http.post(this.BASE_URL + btoa(endpoint), btoa(JSON.stringify(data))).toPromise();
    return response;
  }
}
