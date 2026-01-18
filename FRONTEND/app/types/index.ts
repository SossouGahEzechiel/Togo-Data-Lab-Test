// Enums correspondant à Laravel
export enum VehicleType {
  CAR = 'car',
  MOTORCYCLE = 'motorcycle',
  TRUCK = 'truck',
  VAN = 'van',
  BUS = 'bus'
}

export enum VehicleStatus {
  AVAILABLE = 'available',
  RESERVED = 'reserved',
  SUSPENDED = 'suspended',
  UNDER_REPAIR = 'under_repair',
  UNAVAILABLE = 'unavailable'
}

export enum ReservationStatus {
  PENDING = 'pending',
  VALIDATED = 'validated',
  REJECTED = 'rejected',
  PASSED = 'passed'
}

export enum UserRole {
  ADMIN = 'admin',
  USER = 'user',
  MODERATOR = 'moderator'
}

// Interfaces principales
export interface User {
  id: string
  firstName: string
  lastName: string
  email: string
  role: UserRole
  phone?: string
  isActive: boolean
  image?: Image
  token?: string
}

export interface Image {
  path: string
}

export interface Vehicle {
  id: string
  brand: string
  model: string
  type: VehicleType
  registrationNumber: string
  registrationDate: string
  seatsCount: number
  status: VehicleStatus
  reason?: string
  images?: Image[]
}

export interface Mission {
  id: string
  label: string
  description: string
  from: string
  to?: string
  vehicles?: Vehicle[]
  users?: User[]
  drivers?: User[]
}

export interface Reservation {
  id: string
  from: string
  to?: string
  missionId: string
  vehicleId: string
  driverId?: string
  userId: string
  status: ReservationStatus
  returnDate?: string
  createdAt: string
  updatedAt: string

  // Relations (optionnelles)
  mission?: Mission
  vehicle?: Vehicle
  driver?: User
  user?: User
}

export interface PaginatedResponse<T> {
  data: T[]
  links: PaginationLinks
  meta: PaginationMeta
}

export interface PaginationLinks {
  first: string
  last: string
  prev: string | null
  next: string | null
}

export interface PaginationMeta {
  current_page: number
  from: number
  last_page: number
  links: any[]
  path: string
  per_page: number
  to: number
  total: number
}

export interface ApiResponse<T> {
  data?: T
  message?: string
  errors?: Record<string, string[]>
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface CreateVehicleData {
  brand: string
  model: string
  type: VehicleType
  registrationNumber: string
  registrationDate: string
  seatsCount: number
  status: VehicleStatus
  reason?: string
  images?: File[]
}

export interface CreateReservationData {
  from: string
  to?: string
  missionId: string
  vehicleId: string
  driverId?: string
}

export interface ReservationDecisionData {
  status: ReservationStatus.VALIDATED | ReservationStatus.REJECTED
}
