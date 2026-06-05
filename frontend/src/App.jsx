import { createBrowserRouter, Navigate, RouterProvider } from 'react-router-dom'
import Layout from './components/Layout'
import HotelsListPage from './pages/HotelsListPage'
import HotelFormPage from './pages/HotelFormPage'
import HotelDetailPage from './pages/HotelDetailPage'

const router = createBrowserRouter([
  {
    element: <Layout />,
    children: [
      { path: '/', element: <Navigate to="/hotels" replace /> },
      { path: '/hotels', element: <HotelsListPage /> },
      { path: '/hotels/new', element: <HotelFormPage /> },
      { path: '/hotels/:id', element: <HotelDetailPage /> },
      { path: '/hotels/:id/edit', element: <HotelFormPage /> },
    ],
  },
])

export default function App() {
  return <RouterProvider router={router} />
}
